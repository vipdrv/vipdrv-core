import { Variable } from "./../../../utils/index";
import { Entity } from "./../../index";

export class WidgetThemeEntity extends Entity {
    siteId: number;
    cssUrl: string;
    buttonImageUrl: string;

    constructor() {
        super();
    }

    initializeFromDto(dto: any): void {
        if (Variable.isNullOrUndefined(dto)) {
            return null;
        }
        let mock: WidgetThemeEntity = <WidgetThemeEntity>dto;
        super.initializeFromDto(dto);
        this.siteId = mock.siteId;
        this.cssUrl = mock.cssUrl;
        this.buttonImageUrl = mock.buttonImageUrl;
    }
}