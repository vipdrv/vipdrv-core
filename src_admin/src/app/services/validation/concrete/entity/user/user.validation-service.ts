import { Injectable } from '@angular/core';
import { ConsoleLogger } from '../../../../../utils/logging/console/console.logger';
import { ObjectValidationService } from '../../../object.validation-service';
import { UserEntity } from '../../../../../entities/main/users/user.entity';
import { IUserValidationService } from './i-user.validation-service';
import { Variable } from '../../../../../utils/variable';

@Injectable()
export class UserValidationService
    extends ObjectValidationService<UserEntity>
    implements IUserValidationService {

    /// ctor
    constructor(logger: ConsoleLogger) {
        super(logger);
        this.logger.logDebug('ExpertValidationService: Service has been constructed.');
    }

    /// methods
    isValid(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            this.isValidUserName(entity);
    }

    isValidUserName(entity: UserEntity): boolean {
        return Variable.isNotNullOrUndefined(entity) &&
            Variable.isNotNullOrUndefinedOrEmptyString(entity.username);
    }

    isValidUserPassword(entity: UserEntity, passwordConfirmation: string): boolean {
        throw new Error("Method not implemented.");
    }

    isValidUserEmail(entity: UserEntity): boolean {
        throw new Error("Method not implemented.");
    }

    isValidUserFirstName(entity: UserEntity): boolean {
        throw new Error("Method not implemented.");
    }

    isValidUserSecondName(entity: UserEntity): boolean {
        throw new Error("Method not implemented.");
    }

    isValidUserPhone(entity: UserEntity): boolean {
        throw new Error("Method not implemented.");
    }

    isValidUserSitesCount(entity: UserEntity): boolean {
        throw new Error("Method not implemented.");
    }

    getInvalidUserNameMessageKey(entity: UserEntity): string {
        return 'validation.users.name';
    }

    getInvalidUserPasswordMessageKey(entity: UserEntity): string {
        return 'validation.users.invalidRepeatPasswordMessage';
    }

    getInvalidUserEmailMessageKey(entity: UserEntity): string {
        return 'validation.users.invalidEmailMessage';
    }

    getInvalidUserFirstNameMessageKey(entity: UserEntity): string {
        return 'validation.users.invalidFirstNameMessage';
    }

    getInvalidUserSecondNameMessageKey(entity: UserEntity): string {
        return 'validation.users.name';
    }

    getInvalidUserPhoneMessageKey(entity: UserEntity): string {
        return 'validation.users.invalidPhoneNumberMessage';
    }

    getInvalidUserSitesCountMessageKey(entity: UserEntity): string {
        return 'validation.users.name';
    }
}