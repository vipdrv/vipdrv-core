import { WidgetThemeEntity, LightEntity } from '../../../../entities/index';
import { ICRUDApiService } from '../../i-crud.api-service';
/// is used to communicate with server's sites controller
export interface IWidgetThemeApiService extends ICRUDApiService<WidgetThemeEntity, number, LightEntity> {
}
