import { ICRUDApiService } from '../../index';
import { WidgetThemeEntity, LightEntity } from '../../../../entities/index';
/// is used to communicate with server's sites controller
export interface IWidgetThemeApiService extends ICRUDApiService<WidgetThemeEntity, number, LightEntity> {
}
