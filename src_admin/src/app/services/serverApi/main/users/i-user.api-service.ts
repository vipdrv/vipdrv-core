import { UserEntity, LightEntity } from './../../../../entities/index';
import { ICRUDApiService } from './../../i-crud.api-service';
/// is used to communicate with server's sites controller
export interface IUserApiService extends ICRUDApiService<UserEntity, number, LightEntity> {
    register(entity: any, invitationCode: string): Promise<void>;
    isUsernameValid(value: string): Promise<boolean>;
}