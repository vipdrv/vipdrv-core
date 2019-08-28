import { Component } from '@angular/core';
import { ILogger, ConsoleLogger } from './../../utils/index';
import { ISiteEntityPolicyService, SiteEntityPolicyService } from './../../services/index';
@Component({
    selector: 'sites',
    styleUrls: ['./sites.scss'],
    templateUrl: './sites.html',
})
export class SitesComponent {
    /// injected dependencies
    protected logger: ILogger;
    protected entityPolicyService: ISiteEntityPolicyService;
    /// ctor
    constructor(logger: ConsoleLogger, entityPolicyService: SiteEntityPolicyService) {
        this.logger = logger;
        this.entityPolicyService = entityPolicyService;
        this.logger.logDebug('SitesComponent: Component has been constructed.');
    }
}
