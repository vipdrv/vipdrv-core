import { BeverageEntity, LightEntity } from '../../../../entities/index';
import { ICRUDXApiService } from '../../i-crudx.api-service';
/// is used to communicate with server's sites controller
export interface IBeverageApiService extends ICRUDXApiService<BeverageEntity, number, LightEntity> {
}