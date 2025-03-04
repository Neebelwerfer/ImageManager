export default {
    currentLevel: -1,
    modals: [],

    openModal(modal) {
        this.currentLevel += 1;
        this.modals[this.currentLevel] = modal;
        modal.level = this.currentLevel;

        if(this.currentLevel > 0)
        {
            this.modals[this.currentLevel - 1].isOpen = false;
        }
        console.log("openened modal: " + modal.name);
    },

    isActive(modal) {
        return modal.name === this.modals[this.currentLevel].name;
    },

    closeActiveModal() {
        let modal = this.modals.pop();
        this.currentLevel -= 1;
        if(this.currentLevel > -1)
        {
            this.modals[this.currentLevel].isOpen = true;
        }
        console.log("closed modal: " + modal.name);
    },

    closeAll(){
        this.modals.forEach(element => {
            element.isOpen = false;
        });

        this.modals = [];
    }
};
