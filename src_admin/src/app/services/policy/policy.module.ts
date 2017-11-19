import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SiteEntityPolicyService } from './concrete/widget/site/siteEntity.policy-service';
@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [
    ],
    exports: [
    ],
    providers: [
        SiteEntityPolicyService
    ]
})
export class PolicyModule {}
