const objSearch = {
    get(obj, key) {
        let keys = key.split('.');

        let ptr = obj;
        let curKey = null;
        while((curKey = keys.shift()) !== undefined) {
            if (!ptr.hasOwnProperty(curKey)) {
                return undefined;
            }
            ptr = ptr[curKey];
        }
        return ptr;
    },
    hasKey(obj, key) {
        return this.get(obj, key) !== undefined;
    }
}

module.exports = objSearch;
