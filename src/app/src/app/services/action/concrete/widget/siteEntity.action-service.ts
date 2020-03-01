import { Injectable } from '@angular/core';
import { SiteEntity } from './../../../../entities/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../../index';
@Injectable()
export class SiteEntityActionService {
    /// injected dependencies
    protected entityPolicy: ISiteEntityPolicyService;
    /// ctor
    constructor(entityPolicy: SiteEntityPolicyService) {
        // super(authService);
        this.entityPolicy = entityPolicy;
    }
    /// methods
    invokeAction(entity: SiteEntity): boolean {
        return true;
    }
    /// predicates
    isAllowed(): boolean {
        return true;
    }
    isDisabled(): boolean {
        return false;
    }
    isProcessing(): boolean {
        return false;
    }
    isAllowedForEntity(entity: SiteEntity): boolean {
        return false;
    }
    isDisabledForEntity(entity: SiteEntity): boolean {
        return false;
    }
    isProcessingForEntity(entity: SiteEntity): boolean {
        return false;
    }
}