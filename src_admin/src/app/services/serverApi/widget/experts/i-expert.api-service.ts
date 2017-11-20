import { ExpertEntity, LightEntity } from '../../../../entities/index';
import { ICRUDXApiService } from '../../i-crudx.api-service';
/// is used to communicate with server's sites controller
export interface IExpertApiService extends ICRUDXApiService<ExpertEntity, number, LightEntity> {
}