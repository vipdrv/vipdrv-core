import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger } from '../../../../utils/index';
import { UserEntity, LightEntity } from '../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { IUserApiService } from './i-user.api-service';
@Injectable()
export class UserApiService extends CRUDApiService<UserEntity, number, LightEntity> implements IUserApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'user');
    }
    /// methods

    /// helpers
    protected createEmptyEntity(): UserEntity {
        return new UserEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
