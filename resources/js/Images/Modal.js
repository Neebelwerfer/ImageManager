export default (name) => ({
    isOpen: false,
    name: name,
    context: [],
    level: 0,

    open(context) {
        if(this.isOpen) return;
        Alpine.store('modalManagement').openModal(this);
        this.isOpen = true;
        this.context = context;
    },

    closeModal() {
        if(!Alpine.store('modalManagement').isActive(this)) console.log('Tried to close not active modal');
        Alpine.store('modalManagement').closeActiveModal();
        this.isOpen = false;
        this.context = [];
    },

    init() {
        document.addEventListener('modalOpen', (event) => {
            if(event.detail.name === this.name)
            {
                this.open(event.detail.context);
            }
        })

        document.addEventListener('modalClose', (event) => {
            if(event.detail.name === this.name)
            {
                this.close();
            }
        })
    }
});
