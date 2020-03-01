export interface IStringKeyedCollection<TValue> {
    add(key: string, value: TValue): void;
    addOrUpdate(key: string, value: TValue, updateFunc: (k: string, v: TValue) => TValue): TValue;
    containsKey(key: string): boolean;
    count(): number;
    item(key: string): TValue;
    keys(): string[];
    remove(key: string): TValue;
    values(): TValue[];
}