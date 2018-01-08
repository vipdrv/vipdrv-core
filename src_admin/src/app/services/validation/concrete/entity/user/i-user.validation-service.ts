import { IValidationService } from '../../../i-validation-service';
import { UserEntity } from '../../../../../entities/main/users/user.entity';

export interface IUserValidationService extends IValidationService<UserEntity> {
    isValidUserName(entity: UserEntity): boolean;
}