export default (selectedImagesArray) => ({
    selectedImages: selectedImagesArray,
    allSelected: false,

    selectAll(select)
    {
        Object.keys(this.selectedImages).forEach(key => {
            this.selectedImages[key] = select;
        });

        this.allSelected = select;
    },

    changeSelectMode()
    {
        this.$wire.editMode = !this.$wire.editMode;

        if(this.$wire.editMode == false)
        {
            this.allSelected(false);
        }
    },

    onClick(uuid, action)
    {
        if(this.$wire.editMode)
        {
            this.selectedImages[uuid] = !this.selectedImages[uuid];
            this.allSelected = Object.keys(this.selectedImages).every(key => this.selectedImages[key] === true);
        }
        else
        {
            action();
        }
    }
});
