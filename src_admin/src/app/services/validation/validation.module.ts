import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SiteValidationService } from './concrete/entity/site/site.validation-service';
@NgModule({
    imports: [
        CommonModule
    ],
    declarations: [
    ],
    exports: [
    ],
    providers: [
        SiteValidationService
    ]
})
export class ValidationModule {}
