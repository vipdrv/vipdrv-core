import { Injectable } from '@angular/core';
import { ConsoleLogger } from './../../../../../utils/index';
import { SiteEntity } from './../../../../../entities/index';
import { AuthorizationService } from './../../../../index';
import { ISiteEntityPolicyService } from './i-siteEntity.policy-service';
import { AbstractEntityPolicyService } from '../../../abstractEntity.policy-service';

@Injectable()
export class SiteEntityPolicyService
    extends AbstractEntityPolicyService<SiteEntity>
    implements ISiteEntityPolicyService {

    canGet(): boolean {
        return true;
    }

    canCreate(): boolean {
        return true;
    }

    canUpdate(): boolean {
        return true;
    }

    canDelete(): boolean {
        return true;
    }

    protected innerCanGetEntity(entity: SiteEntity): boolean {
        return true;
    }

    protected innerCanCreateEntity(entity: SiteEntity): boolean {
        return true;
    }

    protected innerCanUpdateEntity(entity: SiteEntity): boolean {
        return true;
    }

    protected innerCanDeleteEntity(entity: SiteEntity): boolean {
        return true;
    }
    /// injected dependencies
    /// ctor
    constructor(logger: ConsoleLogger, authService: AuthorizationService) {
        super(logger, authService);
        this.logger.logDebug('SiteEntityPolicyService: Service has been constructed.');
    }
    /// methods
    canUpdateContacts(): boolean {
        return true;
    }
}