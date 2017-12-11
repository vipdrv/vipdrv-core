import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { ExpertEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { IExpertEntityPolicyService } from './i-expertEntity.policy-service';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';

@Injectable()
export class ExpertEntityPolicyService
    extends AbstractEntityPolicyService<ExpertEntity>
    implements IExpertEntityPolicyService {
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('ExpertEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return undefined;
    }

    canUpdate(): boolean {
        return undefined;
    }

    canDelete(): boolean {
        return undefined;
    }

    protected innerCanGetEntity(entity: ExpertEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: ExpertEntity): boolean {
        return undefined;
    }

    protected innerCanUpdateEntity(entity: ExpertEntity): boolean {
        return undefined;
    }

    protected innerCanDeleteEntity(entity: ExpertEntity): boolean {
        return undefined;
    }

    canUpdateOrder(): boolean {
        return undefined;
    }

    canUpdateActivity(): boolean {
        return undefined;
    }

    canUpdateOrderForEntity(entity: ExpertEntity): boolean {
        return undefined;
    }

    canUpdateActivityForEntity(entity: ExpertEntity): boolean {
        return undefined;
    }
}