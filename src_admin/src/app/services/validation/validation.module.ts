import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RegistrationModelValidationService } from './concrete/registrationModel/registrationModel.validation-service';
import { BeverageValidationService } from './concrete/entity/beverage/beverage.validation-service';
import { ExpertValidationService } from './concrete/entity/expert/expert.validation-service';
import { InvitationValidationService } from './concrete/entity/invitation/invitation.validation-service';
import { LeadValidationService } from './concrete/entity/lead/lead.validation-service';
import { RouteValidationService } from './concrete/entity/route/route.validation-service';
import { SiteValidationService } from './concrete/entity/site/site.validation-service';
import { UserValidationService } from './concrete/entity/user/user.validation-service';
@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [
    ],
    exports: [
    ],
    providers: [
        RegistrationModelValidationService,
        BeverageValidationService,
        ExpertValidationService,
        InvitationValidationService,
        LeadValidationService,
        RouteValidationService,
        SiteValidationService,
        UserValidationService
    ]
})
export class ValidationModule {}
