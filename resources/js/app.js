// Komponen UI kustom berbasis Alpine.
// Didaftarkan saat Alpine (yang dibundel Livewire/Flux) melakukan inisialisasi.
document.addEventListener('alpine:init', () => {
    Alpine.data('uiSelect', (config) => ({
        open: false,
        query: '',
        activeIndex: 0,
        options: config.options || [],
        searchable: config.searchable,
        placeholder: config.placeholder,
        disabled: config.disabled,
        listId: config.listId,
        _entangled: 'model' in config,
        _value: '',
        _typeahead: '',
        _typeaheadTimer: null,

        init() {
            if (this._entangled) {
                // Perubahan dari server (mis. reset filter) tercermin ke tampilan.
                this.$nextTick(() => {
                    Alpine.effect(() => { this._value = String(config.model ?? ''); });
                });
            } else {
                this._value = config.initial ?? '';
            }
        },

        get value() {
            return this._entangled ? String(config.model ?? '') : this._value;
        },
        set value(v) {
            if (this._entangled) {
                config.model = v;
            } else {
                this._value = v;
            }
        },

        selectedLabel() {
            const found = this.options.find((o) => o.value === this.value);
            return found ? found.label : this.placeholder;
        },

        filtered() {
            if (!this.searchable || this.query.trim() === '') return this.options;
            const q = this.query.toLowerCase();
            return this.options.filter((o) => o.label.toLowerCase().includes(q));
        },

        activeId() {
            if (!this.open) return null;
            return this.listId + '-opt-' + this.activeIndex;
        },

        toggle() {
            if (this.disabled) return;
            this.open ? this.close() : this.openPanel();
        },

        openPanel() {
            this.open = true;
            this.query = '';
            const current = this.filtered().findIndex((o) => o.value === this.value);
            this.activeIndex = current >= 0 ? current : 0;
            this.$nextTick(() => {
                if (this.searchable && this.$refs.search) this.$refs.search.focus();
                this.scrollActiveIntoView();
            });
        },

        close() {
            if (!this.open) return;
            this.open = false;
            this.$nextTick(() => this.$refs.trigger?.focus());
        },

        choose(value) {
            this.value = String(value);
            this.close();
        },

        move(delta) {
            const list = this.filtered();
            if (list.length === 0) return;
            this.activeIndex = (this.activeIndex + delta + list.length) % list.length;
            this.scrollActiveIntoView();
        },

        scrollActiveIntoView() {
            this.$nextTick(() => {
                const el = this.$refs.list?.children[this.activeIndex];
                el?.scrollIntoView({ block: 'nearest' });
            });
        },

        chooseActive() {
            const opt = this.filtered()[this.activeIndex];
            if (opt) this.choose(opt.value);
        },

        onTriggerKeydown(e) {
            if (['ArrowDown', 'ArrowUp', 'Enter', ' '].includes(e.key)) {
                e.preventDefault();
                if (!this.open) { this.openPanel(); return; }
            }
            if (this.open) {
                if (e.key === 'ArrowDown') this.move(1);
                else if (e.key === 'ArrowUp') this.move(-1);
                else if (e.key === 'Enter') { e.preventDefault(); this.chooseActive(); }
                else if (e.key === 'Escape') this.close();
            } else if (!this.searchable) {
                this.typeahead(e.key);
            }
        },

        onSearchKeydown(e) {
            if (e.key === 'ArrowDown') { e.preventDefault(); this.move(1); }
            else if (e.key === 'ArrowUp') { e.preventDefault(); this.move(-1); }
            else if (e.key === 'Enter') { e.preventDefault(); this.chooseActive(); }
            else if (e.key === 'Escape') { e.preventDefault(); this.close(); }
        },

        typeahead(key) {
            if (!key || key.length !== 1 || !/\S/.test(key)) return;
            clearTimeout(this._typeaheadTimer);
            this._typeahead += key.toLowerCase();
            const idx = this.options.findIndex((o) => o.label.toLowerCase().startsWith(this._typeahead));
            if (idx >= 0) { this.activeIndex = idx; this.scrollActiveIntoView(); }
            this._typeaheadTimer = setTimeout(() => { this._typeahead = ''; }, 600);
        },
    }));
});
