import { IEntity } from "./i-entity";

export class Entity implements IEntity<number> {
    id: number;
    /// ctor
    constructor(id: number) {
        this.id = id;
    }
    /// mapping
    static map(object: any): Entity {
        return new Entity(object.id);
    }
}