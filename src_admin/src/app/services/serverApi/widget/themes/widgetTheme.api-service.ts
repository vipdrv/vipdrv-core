import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger } from './../../../../utils/index';
import { WidgetThemeEntity, LightEntity } from './../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { IWidgetThemeApiService } from './i-widgetTheme.api-service';
@Injectable()
export class WidgetThemeApiService
    extends CRUDApiService<WidgetThemeEntity, number, LightEntity>
    implements IWidgetThemeApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'widget-theme');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): WidgetThemeEntity {
        return new WidgetThemeEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
