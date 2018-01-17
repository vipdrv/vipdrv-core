import { IValidationService } from '../../../i-validation-service';
import { UserEntity } from '../../../../../entities/main/users/user.entity';

export interface IUserValidationService extends IValidationService<UserEntity> {
    isValidUserName(entity: UserEntity): boolean;
    isValidUserPassword(entity: UserEntity, passwordConfirmation: string): boolean;
    isValidUserEmail(entity: UserEntity): boolean;
    isValidUserFirstName(entity: UserEntity): boolean;
    isValidUserSecondName(entity: UserEntity): boolean;
    isValidUserPhone(entity: UserEntity): boolean;
    isValidUserSitesCount(entity: UserEntity): boolean;
    getInvalidUserNameMessageKey(entity: UserEntity): string;
    getInvalidUserPasswordMessageKey(entity: UserEntity): string;
    getInvalidUserRepeatPasswordMessageKey(entity: UserEntity): string;
    getInvalidUserEmailMessageKey(entity: UserEntity): string;
    getInvalidUserFirstNameMessageKey(entity: UserEntity): string;
    getInvalidUserSecondNameMessageKey(entity: UserEntity): string;
    getInvalidUserPhoneMessageKey(entity: UserEntity): string;
    getInvalidUserSitesCountMessageKey(entity: UserEntity): string;
}