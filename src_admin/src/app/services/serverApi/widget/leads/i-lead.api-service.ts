import { LeadEntity, LightEntity } from '../../../../entities/index';
import { ICRUDApiService } from '../../i-crud.api-service';
/// is used to communicate with server's sites controller
export interface ILeadApiService extends ICRUDApiService<LeadEntity, number, LightEntity> {
}
