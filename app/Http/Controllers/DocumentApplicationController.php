<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentApplicationRequest;
use App\Models\DocumentApplication;
use App\Models\DocumentTemplate;
use App\Models\Penduduk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Record residents' (penduduk) applications for documents and keep them as a searchable history.
 */
class DocumentApplicationController extends Controller
{
    /**
     * Allowed "rows per page" choices on the list.
     *
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [5, 10, 25];

    private const DEFAULT_PER_PAGE = 10;

    /**
     * List the applications, newest first, searchable by applicant name or NIK, document and purpose.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('q')->trim()->toString();

        $perPage = $request->integer('per_page', self::DEFAULT_PER_PAGE);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : self::DEFAULT_PER_PAGE;

        $applications = DocumentApplication::query()
            ->with('user:id,name')
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->whereLike('applicant_name', "%{$search}%")
                ->orWhereLike('applicant_nik', "%{$search}%")
                ->orWhereLike('template_name', "%{$search}%")
                ->orWhereLike('purpose', "%{$search}%")
            ))
            ->latest()
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (DocumentApplication $application): array => [
                'id' => $application->id,
                'applicant_nik' => $application->applicant_nik,
                'applicant_name' => $application->applicant_name,
                'document_template_id' => $application->document_template_id,
                'template_name' => $application->template_name,
                'purpose' => $application->purpose,
                'recorded_by' => $application->user?->name,
                'created_at' => $application->created_at->toIso8601String(),
            ]);

        return Inertia::render('DocumentApplications/Index', [
            'applications' => $applications,
            'search' => $search,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    /**
     * Show the form for recording an application.
     */
    public function create(): Response
    {
        return Inertia::render('DocumentApplications/Create', [
            'templates' => DocumentTemplate::query()
                ->orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (DocumentTemplate $template): array => ['value' => $template->id, 'label' => $template->name]),
            'maxPurposeLength' => StoreDocumentApplicationRequest::MAX_PURPOSE_LENGTH,
        ]);
    }

    /**
     * Record an application, copying the applicant's NIK and name and the template name into it.
     */
    public function store(StoreDocumentApplicationRequest $request): RedirectResponse
    {
        $resident = Penduduk::findOrFail($request->integer('penduduk_id'));
        $template = DocumentTemplate::findOrFail($request->integer('document_template_id'));

        DocumentApplication::create([
            'penduduk_id' => $resident->id,
            'applicant_nik' => $resident->nik,
            'applicant_name' => $resident->nama,
            'document_template_id' => $template->id,
            'template_name' => $template->name,
            'purpose' => $request->string('purpose')->trim()->toString() ?: null,
            'user_id' => $request->user()->id,
        ]);

        Inertia::flash('toast', [
            'title' => 'Pengajuan tercatat',
            'description' => "{$resident->nama} mengajukan \"{$template->name}\".",
            'variant' => 'success',
        ]);

        return to_route('document-applications.index');
    }
}
