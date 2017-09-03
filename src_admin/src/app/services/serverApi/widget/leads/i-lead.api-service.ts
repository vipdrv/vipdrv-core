import { ICRUDApiService } from '../../index';
import { LeadEntity, LightEntity } from '../../../../entities/index';
/// is used to communicate with server's sites controller
export interface ILeadApiService extends ICRUDApiService<LeadEntity, number, LightEntity> {
}
