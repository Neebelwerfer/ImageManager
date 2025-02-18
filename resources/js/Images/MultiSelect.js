export default (selectedImagesArray) => ({
    selectedImages: selectedImagesArray,
    editMode: false,
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
        this.editMode = !this.editMode;

        if(this.editMode == false)
        {
            this.selectedImages = [];
            this.allSelected = false;
        }
    },

    onClick(uuid, action)
    {
        if(this.editMode)
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
