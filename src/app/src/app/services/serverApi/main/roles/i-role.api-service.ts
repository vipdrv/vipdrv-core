import { RoleEntity, LightEntity } from './../../../../entities/index';
import { ICRUDApiService } from './../../i-crud.api-service';
import { GetAllResponse } from './../../dataModels/getAll.response';
/// is used to communicate with server's sites controller
export interface IRoleApiService extends ICRUDApiService<RoleEntity, number, LightEntity> {
    getAllCanBeUsedForInvitation(): Promise<GetAllResponse<RoleEntity>>;
}