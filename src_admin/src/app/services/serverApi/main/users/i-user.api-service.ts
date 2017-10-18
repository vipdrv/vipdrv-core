import { UserEntity, LightEntity, InvitationEntity } from './../../../../entities/index';
import { ICRUDApiService } from './../../i-crud.api-service';
import { GetAllResponse } from './../../dataModels/getAll.response';
/// is used to communicate with server's sites controller
export interface IUserApiService extends ICRUDApiService<UserEntity, number, LightEntity> {
    register(entity: any, invitationCode: string): Promise<void>;
    isUsernameValid(value: string): Promise<boolean>;
    createInvitation(userId: number, entity: InvitationEntity): Promise<InvitationEntity>;
    getInvitations(userId: number, page: number, pageSize: number, sorting: string): Promise<GetAllResponse<InvitationEntity>>;
}