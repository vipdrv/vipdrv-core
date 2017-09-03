import { Entity } from "./entity";
import { ILightEntity } from "./i-light-entity";

export class LightEntity extends Entity implements ILightEntity<number> {
    displayText: string;
    /// ctor
    constructor() {
        super();
    }
    /// mapping
    initializeFromDto(dto: any): void {
        super.initializeFromDto(dto);
        this.displayText = dto.displayText;
    }
}