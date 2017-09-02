import { ICRUDApiService } from "./../i-crud.api-service";
import { SiteEntity } from "./../../../entities/sites/site.entity";
import { LightEntity } from "./../../../entities/lightEntity";
/// is used to communicate with server's site controller
export interface ISiteApiService extends ICRUDApiService<SiteEntity, number, LightEntity> {}