import { IStringKeyedCollection } from "./i-string-keyed-collection";

export class StringKeyedCollection<TValue> implements IStringKeyedCollection<TValue> {
    private _items: { [index: string]: TValue } = {};

    private _count: number = 0;

    public containsKey(key: string): boolean {
        return this._items.hasOwnProperty(key);
    }

    public count(): number {
        return this._count;
    }

    public add(key: string, value: TValue): void {
        if (!this.containsKey(key)) {
            this._count++;
        }
        this._items[key] = value;
    }

    public addOrUpdate(
        key: string,
        value: TValue,
        updateFunc: (k: string, v: TValue) => TValue): TValue {
        if (this.containsKey(key)) {
            this._items[key] = updateFunc(key, this.item(key));
        } else {
            this.add(key, value);
        }
        return this.item(key);
    }

    public remove(key: string): TValue {
        let val: TValue = this._items[key];
        if (this.containsKey(key)) {
            delete this._items[key];
            this._count--;
        }
        return val;
    }

    public item(key: string): TValue {
        return this._items[key];
    }

    public keys(): string[] {
        let keys: string[] = [];
        for (let property in this._items) {
            if (this._items.hasOwnProperty(property)) {
                keys.push(property);
            }
        }
        return keys;
    }

    public values(): TValue[] {
        let values: TValue[] = [];
        for (let property in this._items) {
            if (this._items.hasOwnProperty(property)) {
                values.push(this._items[property]);
            }
        }
        return values;
    }
}