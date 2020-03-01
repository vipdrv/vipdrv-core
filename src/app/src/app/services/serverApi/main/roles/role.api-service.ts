import { Injectable } from '@angular/core';
import { ConsoleLogger, Variable } from './../../../../utils/index';
import { HttpXService } from './../../../index';
import { RoleEntity, LightEntity } from '../../../../entities/index';
import { CRUDApiService } from './../../crud.api-service';
import { IRoleApiService } from './i-role.api-service';
import { GetAllResponse } from './../../dataModels/getAll.response';
@Injectable()
export class RoleApiService extends CRUDApiService<RoleEntity, number, LightEntity> implements IRoleApiService {
    /// ctor
    constructor(httpService: HttpXService, logger: ConsoleLogger) {
        super(httpService, logger, 'role');
        this.logger.logDebug('RoleApiService: Service has been constructed.');
    }
    /// methods
    getAllCanBeUsedForInvitation(): Promise<GetAllResponse<RoleEntity>> {
        return this.getAll(0, 500, 'name asc', { canBeUsedForInvitation: true });
    }
    /// helpers
    protected createEmptyEntity(): RoleEntity {
        return new RoleEntity();
    }
    protected createEmptyLightEntity(): LightEntity {
        return new LightEntity();
    }
}