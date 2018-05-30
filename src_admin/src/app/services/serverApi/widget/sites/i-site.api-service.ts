import { SiteEntity, LightEntity } from '../../../../entities/index';
import { ICRUDApiService } from '../../i-crud.api-service';
/// is used to communicate with server's sites controller
export interface ISiteApiService extends ICRUDApiService<SiteEntity, number, LightEntity> {
    patchContacts(id: number, value: any): Promise<void>;
}
