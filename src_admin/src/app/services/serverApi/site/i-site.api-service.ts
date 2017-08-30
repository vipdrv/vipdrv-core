import { ICRUDApiService } from "./../i-crud.api-service";
import { SiteEntity } from "./../../../entities/sites/site.entity";
import { GetAllRequest } from "./../dataModels/getAll.request";
import { GetAllResponse } from "./../dataModels/getAll.response";
/// is used to communicate with server's site controller
export interface ISiteApiService extends ICRUDApiService<SiteEntity, number> {

}