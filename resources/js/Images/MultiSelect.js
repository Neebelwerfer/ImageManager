export default (selectedImagesArray) => ({
    selectedImages: selectedImagesArray,
    selected: 0,

    selectAll(select)
    {
        Object.keys(this.selectedImages).forEach(key => {
            this.selectedImages[key] = select;
        });

        this.selected = select ? this.selectedImages.length : 0;
        this.allSelected = select;
    },

    changeSelectMode()
    {
        this.$wire.editMode = !this.$wire.editMode;

        if(this.$wire.editMode == false)
        {
            this.selectAll(false);
        }
    },

    onClick(index, action)
    {
        if(this.$wire.editMode)
        {
            let wasSelected = this.selectedImages[index]
            this.selectedImages[index] = !wasSelected;

            this.selected += wasSelected ? -1 : 1;
        }
        else
        {
            action();
        }
    },

    isAllSelected(){
        return this.selected == this.selectedImages.length;
    },

    isNoneSelected(){
        return this.selected == 0;
    }
});
