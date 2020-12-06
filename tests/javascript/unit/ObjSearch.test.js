const ObjSearch = require('../../../resources/javascript/lib/ObjSearch');

let targetObj = {
    'some': {
        'key': {
            'here': 'value'
        }
    }
};

test('it returns a nested key value', () => {
    expect(ObjSearch.get(targetObj, 'some.key.here')).toBe('value');
});

test('it returns undefined for a missing key', () => {
    expect(ObjSearch.get(targetObj, 'some.other.place.nowhere')).toBe(undefined);
});

test('it returns undefined for a weird key', () => {
    expect(ObjSearch.get(targetObj, '.')).toBe(undefined);
});

test('it returns undefined for no key', () => {
    expect(ObjSearch.get(targetObj, '')).toBe(undefined);
});

test('it can find a nested key', () => {
    expect(ObjSearch.hasKey(targetObj, 'some.key')).toBe(true);
});

test('it does not find a nested key that does not exist', () => {
    expect(ObjSearch.hasKey(targetObj, 'some.key.that.does.not-exist')).toBe(false);
});
