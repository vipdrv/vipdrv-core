import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SiteEntityPolicyService } from './concrete/widget/site/siteEntity.policy-service';
import { LeadEntityPolicyService } from './concrete/widget/lead/leadEntity.policy-service';
@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [
    ],
    exports: [
    ],
    providers: [
        SiteEntityPolicyService,
        LeadEntityPolicyService
    ]
})
export class PolicyModule {}
