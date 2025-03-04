export default {
    currentLevel: -1,
    modals: [],

    openModal(modal) {
        this.currentLevel += 1;
        this.modals[this.currentLevel] = modal;
        modal.level = this.currentLevel;
        console.log('opening ' + modal.name);
        if(this.currentLevel > 0)
        {
            this.modals[this.currentLevel - 1].isOpen = false;
        }
    },

    isActive(modal) {
        return modal.name === this.modals[this.currentLevel].name;
    },

    closeActiveModal() {
        this.modals.pop();
        this.currentLevel -= 1;
        if(this.currentLevel > -1)
        {
            this.modals[this.currentLevel].isOpen = true;
        }
    },

    closeAll(){
        this.modals.forEach(element => {
            element.isOpen = false;
        });

        this.modals = [];
    }
};
