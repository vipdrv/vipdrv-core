import { Injectable } from '@angular/core';
import { HttpService, ConsoleLogger, Variable } from '../../../../utils/index';
import { UserEntity, LightEntity, InvitationEntity } from '../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { IUserApiService } from './i-user.api-service';
import { GetAllResponse } from './../../dataModels/getAll.response';
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
    createInvitation(userId: number, entity: InvitationEntity): Promise<InvitationEntity> {
        return this.httpService
            .post(this.createUrlWithMethodName(`${userId}/invitation`), entity)
            .then(function (response: InvitationEntity): InvitationEntity {
                const responseEntity: InvitationEntity = new InvitationEntity();
                responseEntity.initializeFromDto(response);
                return responseEntity;
            });
    }
    getInvitations(userId: number, page: number, pageSize: number, sorting: string): Promise<GetAllResponse<InvitationEntity>> {
        const self = this;
        const methodName: string = `${userId}/invitation`;
        const pageParameter = new UrlParameter('page', page);
        const pageSizeParameter = new UrlParameter('pageSize', pageSize);
        const sortingParameter = new UrlParameter('sorting', pageSize);
        return self.httpService
            .get(self.createUrlWithMethodNameAndUrlParams(methodName, pageParameter, pageSizeParameter, sortingParameter))
            .then(function (response: any): GetAllResponse<InvitationEntity> {
                if (Variable.isNullOrUndefined(response)) {
                    throw new Error('GetInvitations method returns not defined response.');
                }
                if (Variable.isNullOrUndefined(response.items)) {
                    throw new Error('GetInvitations method returns response with not defined items.');
                }
                const entities: Array<InvitationEntity> = new Array<InvitationEntity>();
                response.items.forEach(function (item: any): void {
                    const entity: InvitationEntity = self.createEmptyInvitationEntity();
                    entity.initializeFromDto(item);
                    entities.push(entity);
                });
                return new GetAllResponse<InvitationEntity>(response.totalCount, entities);
            });
    }
    /// helpers
    protected createEmptyEntity(): UserEntity {
        return new UserEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
    protected createEmptyInvitationEntity(): InvitationEntity {
        return new InvitationEntity();
    }
}
