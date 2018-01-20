import { UserEntity, LightEntity, InvitationEntity } from './../../../../entities/index';
import { ICRUDApiService } from './../../i-crud.api-service';
import { GetAllResponse } from './../../dataModels/getAll.response';
/// is used to communicate with server's sites controller
export interface IUserApiService extends ICRUDApiService<UserEntity, number, LightEntity> {
    register(entity: any, invitationCode: string): Promise<void>;
    isUsernameValid(value: string): Promise<boolean>;
    createInvitation(entity: InvitationEntity): Promise<InvitationEntity>;
    getInvitations(userId: number, page: number, pageSize: number, sorting: string): Promise<GetAllResponse<InvitationEntity>>;
    patchPassword(userId: number, oldPassword: string, newPassword: string): Promise<void>;
    patchAvatar(userId: number, newAvatarUrl: string): Promise<void>;
    patchPersonalInfo(userId: number, firstName: string, secondName: string, email: string, phoneNumber: string): Promise<void>;
    deleteInvitation(id: number): Promise<void>;
}