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
    getInvalidUserNameMessageKey(entity: UserEntity): string {
        return 'validation.experts.name';
    }
}