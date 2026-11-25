<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenerateDocumentRequest;
use App\Http\Requests\StoreDocumentTemplateRequest;
use App\Http\Requests\UpdateDocumentTemplateRequest;
use App\Models\DocumentTemplate;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\Jc;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Upload Word templates with `${variable}` placeholders and download them filled in.
 */
class DocumentTemplateController extends Controller
{
    /**
     * Allowed "rows per page" choices on the list.
     *
     * @var list<int>
     */
    private const PER_PAGE_OPTIONS = [5, 10, 25];

    private const DEFAULT_PER_PAGE = 10;

    /**
     * List the uploaded templates, searchable by name, file name or variable.
     */
    public function index(Request $request): Response
    {
        $search = $request->string('q')->trim()->toString();

        $perPage = $request->integer('per_page', self::DEFAULT_PER_PAGE);
        $perPage = in_array($perPage, self::PER_PAGE_OPTIONS, true) ? $perPage : self::DEFAULT_PER_PAGE;

        $templates = DocumentTemplate::query()
            ->with(['user:id,name', 'updater:id,name'])
            ->when($search !== '', fn (Builder $query) => $query->where(fn (Builder $query) => $query
                ->whereLike('name', "%{$search}%")
                ->orWhereLike('original_filename', "%{$search}%")
                ->orWhereLike('variables', "%{$search}%")
            ))
            ->latest()
            ->latest('id')
            ->paginate($perPage)
            ->withQueryString()
            ->through(fn (DocumentTemplate $template): array => $this->templateData($template, $request));

        return Inertia::render('DocumentTemplates/Index', [
            'templates' => $templates,
            'search' => $search,
            'perPage' => $perPage,
            'perPageOptions' => self::PER_PAGE_OPTIONS,
        ]);
    }

    /**
     * Show the upload form for a new template.
     */
    public function create(): Response
    {
        return Inertia::render('DocumentTemplates/Create', [
            'maxFileSizeMb' => StoreDocumentTemplateRequest::MAX_FILE_SIZE_KB / 1024,
        ]);
    }

    /**
     * Store an uploaded template and go back to the list, with a success toast.
     */
    public function store(StoreDocumentTemplateRequest $request): RedirectResponse
    {
        $file = $request->file('file');
        $originalFilename = $file->getClientOriginalName();

        $template = DocumentTemplate::create([
            'user_id' => $request->user()->id,
            'name' => $request->string('name')->trim()->toString() ?: pathinfo($originalFilename, PATHINFO_FILENAME),
            'original_filename' => $originalFilename,
            'path' => $file->store(DocumentTemplate::STORAGE_DIRECTORY, DocumentTemplate::STORAGE_DISK),
            'size' => $file->getSize(),
            'variables' => $request->templateVariables(),
        ]);

        Inertia::flash('toast', [
            'title' => 'Template berhasil diunggah',
            'description' => "\"{$template->name}\" tersimpan dengan ".count($template->variables).' variabel.',
            'variant' => 'success',
        ]);

        return to_route('document-templates.index');
    }

    /**
     * Show the form for filling in a template's variables.
     */
    public function show(Request $request, DocumentTemplate $documentTemplate): Response
    {
        $documentTemplate->load(['user:id,name', 'updater:id,name']);

        return Inertia::render('DocumentTemplates/Show', [
            'template' => [
                ...$this->templateData($documentTemplate, $request),
                'fields' => collect($documentTemplate->variables)
                    ->map(fn (string $variable): array => ['name' => $variable, 'label' => DocumentTemplate::labelFor($variable)])
                    ->all(),
            ],
        ]);
    }

    /**
     * Fill the template with the submitted values and download the result as .docx.
     */
    public function generate(GenerateDocumentRequest $request, DocumentTemplate $documentTemplate): BinaryFileResponse
    {
        abort_unless(Storage::disk(DocumentTemplate::STORAGE_DISK)->exists($documentTemplate->path), 404);

        $path = $documentTemplate->generate($request->validated('values'));
        $fileName = (Str::slug($documentTemplate->name) ?: 'dokumen').'-'.now()->format('Ymd-His').'.docx';

        return response()->download($path, $fileName)->deleteFileAfterSend();
    }

    /**
     * Download the original, unfilled template.
     */
    public function download(DocumentTemplate $documentTemplate): StreamedResponse
    {
        $disk = Storage::disk(DocumentTemplate::STORAGE_DISK);

        abort_unless($disk->exists($documentTemplate->path), 404);

        return $disk->download($documentTemplate->path, $documentTemplate->original_filename);
    }

    /**
     * Rename a template and go back to the page it was renamed from. Only the uploader may do this.
     */
    public function update(UpdateDocumentTemplateRequest $request, DocumentTemplate $documentTemplate): RedirectResponse
    {
        $documentTemplate->update([
            'name' => $request->string('name')->trim()->toString(),
            'updated_by_id' => $request->user()->id,
        ]);

        Inertia::flash('toast', [
            'title' => 'Nama template diperbarui',
            'description' => "Template sekarang bernama \"{$documentTemplate->name}\".",
            'variant' => 'success',
        ]);

        return back();
    }

    /**
     * Delete a template and its file. Only the uploader may do this.
     */
    public function destroy(DocumentTemplate $documentTemplate): RedirectResponse
    {
        Gate::authorize('delete', $documentTemplate);

        Storage::disk(DocumentTemplate::STORAGE_DISK)->delete($documentTemplate->path);
        $documentTemplate->delete();

        Inertia::flash('success', "Template \"{$documentTemplate->name}\" dihapus.");

        return to_route('document-templates.index');
    }

    /**
     * Download a sample template (surat keterangan) that uses `${nama}`, `${nik}`, `${alamat_lengkap}` and more.
     */
    public function sample(): BinaryFileResponse
    {
        $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'sample-template-'.Str::uuid().'.docx';

        IOFactory::createWriter($this->sampleDocument(), 'Word2007')->save($path);

        return response()->download($path, 'contoh-template-surat-keterangan.docx')->deleteFileAfterSend();
    }

    /**
     * Template fields shared by the list and the fill-in page.
     *
     * @return array{id: int, name: string, original_filename: string, size: int, variables: list<string>, uploaded_by: string|null, uploaded_at: string, was_edited: bool, updated_by: string|null, updated_at: string, can_update: bool, can_delete: bool}
     */
    private function templateData(DocumentTemplate $template, Request $request): array
    {
        return [
            'id' => $template->id,
            'name' => $template->name,
            'original_filename' => $template->original_filename,
            'size' => $template->size,
            'variables' => $template->variables,
            'uploaded_by' => $template->user?->name,
            'uploaded_at' => $template->created_at->toIso8601String(),
            'was_edited' => $template->wasEdited(),
            'updated_by' => $template->updater?->name,
            'updated_at' => $template->updated_at->toIso8601String(),
            'can_update' => $request->user()->can('update', $template),
            'can_delete' => $request->user()->can('delete', $template),
        ];
    }

    private function sampleDocument(): PhpWord
    {
        $phpWord = new PhpWord;
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection(['marginTop' => 1440, 'marginRight' => 1440, 'marginBottom' => 1440, 'marginLeft' => 1440]);
        $centered = ['alignment' => Jc::CENTER];

        $section->addText('SURAT KETERANGAN', ['bold' => true, 'size' => 14, 'underline' => 'single'], $centered);
        $section->addText('Nomor: ${nomor_surat}', [], [...$centered, 'spaceAfter' => 360]);
        $section->addText('Yang bertanda tangan di bawah ini menerangkan bahwa:', [], ['spaceAfter' => 120]);

        $table = $section->addTable(['cellMargin' => 40]);

        foreach (['Nama' => '${nama}', 'NIK' => '${nik}', 'Alamat' => '${alamat_lengkap}'] as $label => $variable) {
            $table->addRow();
            $table->addCell(2000)->addText($label);
            $table->addCell(300)->addText(':');
            $table->addCell(6700)->addText($variable);
        }

        $section->addText(
            'Adalah benar warga yang berdomisili di alamat tersebut. Demikian surat keterangan ini dibuat untuk dipergunakan sebagaimana mestinya.',
            [],
            ['spaceBefore' => 240, 'alignment' => Jc::BOTH],
        );

        $signature = ['alignment' => Jc::END];
        $section->addText('${tanggal_surat}', [], [...$signature, 'spaceBefore' => 480]);
        $section->addText('${nama_penandatangan}', ['bold' => true], [...$signature, 'spaceBefore' => 960]);

        return $phpWord;
    }
}
