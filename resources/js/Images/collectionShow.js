export default (count, gridView) => ({
    count: count,
    gridView: gridView,

    show(id){
        this.count = id;
        this.gridView = false;
    }

});
