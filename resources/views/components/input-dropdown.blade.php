@props([
    'name' => '',
    'label' => '',
    'options' => [],
    'value' => '',
    'required' => false,
    'readonly' => false,
    'error' => null,
    'class' => '',
    'buttonClass' =>
        'px-5 py-2.5 rounded-lg border border-gray-300 cursor-pointer text-slate-900 text-sm font-medium outline-none bg-white hover:bg-gray-50 w-full text-left flex items-center justify-between',
    'menuClass' =>
        'absolute left-0 rounded-lg [box-shadow:0_8_19px_-7px_rgba(215,215,215,1)] bg-white py-2 z-[1000] w-full divide-y divide-gray-200 max-h-96 overflow-auto',
    'optionClass' => 'px-5 py-2.5 hover:bg-gray-50 text-slate-600 text-sm font-medium cursor-pointer',
])

<div {{ $attributes->merge(['class' => "space-y-1 {$class}"]) }} x-data="dropdownComponent(@js($value), @js($options))" x-modelable="selected"
    x-init="init()">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
            @if ($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif

    <input type="hidden" name="{{ $name }}" x-model="selected" @if ($required) required @endif>

    <div class="relative" @click.outside="open = false" x-ref="container">
        <button type="button" @click="toggleDropdown()" :class="{ 'ring-2 ring-blue-500': open }"
            class="{{ $buttonClass }} @if ($error) border-red-500 @endif @if ($readonly) cursor-not-allowed pointer-events-none @endif">
            <span x-text="selectedLabel"></span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3 fill-gray-500 inline"
                :style="{ transform: open ? 'rotate(180deg)' : 'rotate(0deg)' }" style="transition: transform 0.2s;"
                viewBox="0 0 24 24">
                <path fill-rule="evenodd"
                    d="M11.99997 18.1669a2.38 2.38 0 0 1-1.68266-.69733l-9.52-9.52a2.38 2.38 0 1 1 3.36532-3.36532l7.83734 7.83734 7.83734-7.83734a2.38 2.38 0 1 1 3.36532 3.36532l-9.52 9.52a2.38 2.38 0 0 1-1.68266.69734z"
                    clip-rule="evenodd" data-original="#000000" />
            </svg>
        </button>

        <ul x-show="open" x-transition
            :class="[
                '{{ $menuClass }}',
                openUp ? 'bottom-full mb-1' : 'top-full mt-1'
            ]"
            x-ref="menu">
            @foreach ($options as $option)
                <li @click="selectOption('{{ $option['value'] }}', '{{ $option['label'] }}')"
                    class="{{ $optionClass }}">
                    {{ $option['label'] }}
                </li>
            @endforeach
        </ul>
    </div>

    @if ($error)
        <p class="text-sm text-red-600">{{ $error }}</p>
    @endif
</div>

@push('scripts')
    <script>
        function dropdownComponent(initialValue = '', options = []) {
            return {
                open: false,
                openUp: false,
                selected: initialValue,
                selectedLabel: '',
                options: options,

                init() {
                    this.updateSelectedLabel();

                    this.$watch('open', (value) => {
                        if (value) {
                            this.$nextTick(() => {
                                this.checkSpaceAndPosition();
                            });
                        }
                    });

                    this.$watch('selected', () => {
                        this.updateSelectedLabel();
                    });
                },

                toggleDropdown() {
                    this.open = !this.open;
                },

                selectOption(value, label) {
                    this.selected = value;
                    this.selectedLabel = label;
                    this.open = false;
                },

                updateSelectedLabel() {
                    const option = this.options.find(o => String(o.value) === String(this.selected));
                    this.selectedLabel = option ? option.label : 'Pilih...';
                },

                checkSpaceAndPosition() {
                    this.$nextTick(() => {
                        const container = this.$refs.container;
                        const menu = this.$refs.menu;

                        if (!container || !menu) return;

                        const containerRect = container.getBoundingClientRect();
                        const menuHeight = menu.offsetHeight;
                        const spaceBelow = window.innerHeight - (containerRect.bottom + 8);
                        const spaceAbove = containerRect.top - 8;

                        if (spaceBelow < menuHeight && spaceAbove > spaceBelow) {
                            this.openUp = true;
                        } else {
                            this.openUp = false;
                        }
                    });
                },
            };
        }
    </script>
@endpush
