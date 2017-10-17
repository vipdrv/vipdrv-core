import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger } from '../../../../utils/index';
import { UserEntity, LightEntity } from '../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { IUserApiService } from './i-user.api-service';
import { UrlParameter } from './../../urlParameter';
@Injectable()
export class UserApiService extends CRUDApiService<UserEntity, number, LightEntity> implements IUserApiService {
    /// ctor
    constructor(
        httpService: HttpService,
        logger: ConsoleLogger) {
        super(httpService, logger, 'user');
    }
    /// methods
    register(entity: any, invitationCode: string): Promise<void> {
        return this.httpService
            .post(this.createUrlWithMethodName(invitationCode), entity)
            .then(function (response: any): Promise<void> {
                return Promise.resolve();
            });
    }
    isUsernameValid(value: string): Promise<boolean> {
        return this.httpService
            .get(this.createUrlWithMethodName(`is-username-valid/${value}`))
            .then(function (response: boolean): Promise<boolean> {
                return Promise.resolve(response);
            });
    }
    /// helpers
    protected createEmptyEntity(): UserEntity {
        return new UserEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}
