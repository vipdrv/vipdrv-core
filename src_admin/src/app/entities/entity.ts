import { IEntity } from "./i-entity";

export class Entity implements IEntity<number> {
    id: number;
    /// ctor
    constructor() {}
    /// mapping
    initializeFromDto(dto: any): void {
        this.id = dto.id;
    }
}