<?php

namespace App\Enums;

/**
 * Every reka-ui component documented under /components.
 *
 * The case value is the URL slug and route name suffix (`components.{value}`),
 * the case name is the Vue page in `resources/js/pages/components/{Name}.vue`.
 */
enum UiComponent: string
{
    case Accordion = 'accordion';
    case AlertDialog = 'alert-dialog';
    case AspectRatio = 'aspect-ratio';
    case Autocomplete = 'autocomplete';
    case Avatar = 'avatar';
    case Calendar = 'calendar';
    case Checkbox = 'checkbox';
    case Collapsible = 'collapsible';
    case ColorArea = 'color-area';
    case ColorField = 'color-field';
    case ColorSlider = 'color-slider';
    case ColorSwatch = 'color-swatch';
    case ColorSwatchPicker = 'color-swatch-picker';
    case Combobox = 'combobox';
    case ContextMenu = 'context-menu';
    case DateField = 'date-field';
    case DatePicker = 'date-picker';
    case DateRangeField = 'date-range-field';
    case DateRangePicker = 'date-range-picker';
    case Dialog = 'dialog';
    case Drawer = 'drawer';
    case DropdownMenu = 'dropdown-menu';
    case Editable = 'editable';
    case Field = 'field';
    case Form = 'form';
    case HoverCard = 'hover-card';
    case Label = 'label';
    case Listbox = 'listbox';
    case Menubar = 'menubar';
    case MonthPicker = 'month-picker';
    case MonthRangePicker = 'month-range-picker';
    case NavigationMenu = 'navigation-menu';
    case NumberField = 'number-field';
    case Pagination = 'pagination';
    case PinInput = 'pin-input';
    case Popover = 'popover';
    case Progress = 'progress';
    case RadioGroup = 'radio-group';
    case RangeCalendar = 'range-calendar';
    case Rating = 'rating';
    case ScrollArea = 'scroll-area';
    case Select = 'select';
    case Separator = 'separator';
    case Slider = 'slider';
    case Splitter = 'splitter';
    case Stepper = 'stepper';
    case Switch = 'switch';
    case Tabs = 'tabs';
    case TagGroup = 'tag-group';
    case TagsInput = 'tags-input';
    case TimeField = 'time-field';
    case TimeRangeField = 'time-range-field';
    case Toast = 'toast';
    case Toggle = 'toggle';
    case ToggleGroup = 'toggle-group';
    case Toolbar = 'toolbar';
    case Tooltip = 'tooltip';
    case Tree = 'tree';
    case YearPicker = 'year-picker';
    case YearRangePicker = 'year-range-picker';

    /**
     * Get the display name, e.g. "Alert Dialog".
     */
    public function label(): string
    {
        return trim(preg_replace('/(?<!^)[A-Z]/', ' $0', $this->name));
    }

    /**
     * Get the Inertia page component name.
     */
    public function page(): string
    {
        return 'components/'.$this->name;
    }

    /**
     * Get the named route for this component's documentation page.
     */
    public function routeName(): string
    {
        return 'components.'.$this->value;
    }

    /**
     * Get the official reka-ui documentation URL.
     */
    public function docsUrl(): string
    {
        return 'https://reka-ui.com/docs/components/'.$this->value;
    }

    /**
     * Get a short Indonesian description of the component.
     */
    public function description(): string
    {
        return match ($this) {
            self::Accordion => 'Sekumpulan panel yang bisa dibuka-tutup secara vertikal.',
            self::AlertDialog => 'Dialog konfirmasi yang menghentikan alur dan menunggu jawaban pengguna.',
            self::AspectRatio => 'Menjaga konten tetap pada rasio lebar dan tinggi tertentu.',
            self::Autocomplete => 'Input teks bebas dengan daftar saran yang muncul saat mengetik.',
            self::Avatar => 'Foto profil dengan cadangan inisial ketika gambar gagal dimuat.',
            self::Calendar => 'Kalender untuk memilih satu tanggal.',
            self::Checkbox => 'Kotak centang untuk pilihan ya/tidak.',
            self::Collapsible => 'Satu panel yang bisa disembunyikan dan ditampilkan.',
            self::ColorArea => 'Area dua dimensi untuk memilih saturasi dan kecerahan warna.',
            self::ColorField => 'Input untuk mengetik nilai warna (hex atau kanal tertentu).',
            self::ColorSlider => 'Slider untuk mengatur satu kanal warna, misalnya hue atau alpha.',
            self::ColorSwatch => 'Kotak kecil yang menampilkan sebuah warna.',
            self::ColorSwatchPicker => 'Pilihan warna dari sekumpulan swatch.',
            self::Combobox => 'Select yang bisa dicari dengan mengetik.',
            self::ContextMenu => 'Menu yang muncul saat klik kanan.',
            self::DateField => 'Input tanggal yang diketik per segmen hari, bulan, dan tahun.',
            self::DatePicker => 'Input tanggal dengan kalender popover.',
            self::DateRangeField => 'Input rentang tanggal yang diketik per segmen.',
            self::DateRangePicker => 'Input rentang tanggal dengan kalender popover.',
            self::Dialog => 'Jendela modal di atas halaman untuk form atau informasi.',
            self::Drawer => 'Panel yang meluncur dari tepi layar dan bisa ditutup dengan swipe.',
            self::DropdownMenu => 'Menu aksi yang muncul dari sebuah tombol.',
            self::Editable => 'Teks yang bisa langsung diedit di tempat.',
            self::Field => 'Pembungkus input dengan label, deskripsi, dan pesan validasi.',
            self::Form => 'Form dengan validasi bawaan browser yang terhubung ke Field.',
            self::HoverCard => 'Kartu pratinjau yang muncul saat kursor diarahkan ke sebuah link.',
            self::Label => 'Label yang terhubung ke sebuah kontrol form.',
            self::Listbox => 'Daftar pilihan yang selalu terlihat, bisa satu atau banyak.',
            self::Menubar => 'Bilah menu horizontal seperti aplikasi desktop.',
            self::MonthPicker => 'Pemilih bulan dalam satu tahun.',
            self::MonthRangePicker => 'Pemilih rentang bulan.',
            self::NavigationMenu => 'Menu navigasi situs dengan panel konten yang bisa dibuka.',
            self::NumberField => 'Input angka dengan tombol tambah dan kurang.',
            self::Pagination => 'Navigasi antar halaman data.',
            self::PinInput => 'Input kode OTP atau PIN per karakter.',
            self::Popover => 'Konten mengambang yang muncul saat sebuah tombol diklik.',
            self::Progress => 'Indikator kemajuan sebuah proses.',
            self::RadioGroup => 'Sekumpulan pilihan di mana hanya satu yang bisa dipilih.',
            self::RangeCalendar => 'Kalender untuk memilih rentang tanggal.',
            self::Rating => 'Penilaian dengan bintang.',
            self::ScrollArea => 'Area scroll dengan scrollbar kustom yang konsisten di semua browser.',
            self::Select => 'Pilihan dari daftar opsi dalam dropdown.',
            self::Separator => 'Garis pemisah antar konten.',
            self::Slider => 'Penggeser untuk memilih nilai atau rentang angka.',
            self::Splitter => 'Panel yang ukurannya bisa diubah dengan menyeret pembatas.',
            self::Stepper => 'Penanda langkah dalam proses bertahap.',
            self::Switch => 'Saklar on/off.',
            self::Tabs => 'Konten bertab, hanya satu panel yang tampil dalam satu waktu.',
            self::TagGroup => 'Sekumpulan tag yang bisa dipilih atau dihapus.',
            self::TagsInput => 'Input untuk menambahkan banyak tag.',
            self::TimeField => 'Input waktu yang diketik per segmen jam dan menit.',
            self::TimeRangeField => 'Input rentang waktu.',
            self::Toast => 'Notifikasi singkat yang muncul lalu hilang sendiri.',
            self::Toggle => 'Tombol dengan status aktif/nonaktif.',
            self::ToggleGroup => 'Sekumpulan tombol toggle, satu atau banyak yang aktif.',
            self::Toolbar => 'Bilah berisi tombol dan toggle yang dikelompokkan.',
            self::Tooltip => 'Keterangan singkat saat kursor diarahkan ke sebuah elemen.',
            self::Tree => 'Daftar bertingkat yang bisa dibuka-tutup, seperti struktur folder.',
            self::YearPicker => 'Pemilih tahun.',
            self::YearRangePicker => 'Pemilih rentang tahun.',
        };
    }

    /**
     * Get the data a page or the sidebar needs about this component.
     *
     * @return array{slug: string, label: string, description: string, route: string, docsUrl: string}
     */
    public function toArray(): array
    {
        return [
            'slug' => $this->value,
            'label' => $this->label(),
            'description' => $this->description(),
            'route' => $this->routeName(),
            'docsUrl' => $this->docsUrl(),
        ];
    }

    /**
     * Get every component, in alphabetical order.
     *
     * @return list<array{slug: string, label: string, description: string, route: string, docsUrl: string}>
     */
    public static function all(): array
    {
        return array_map(fn (self $component): array => $component->toArray(), self::cases());
    }
}
