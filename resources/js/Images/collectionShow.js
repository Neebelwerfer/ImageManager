export default (count) => ({
    gridView: true,
    count: count,

    show(id){
        this.count = id;
        this.gridView = false;
    }

});
