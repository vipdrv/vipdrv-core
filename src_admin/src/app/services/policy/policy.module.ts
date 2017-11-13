import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { BasePolicyService } from './base.policy-service';

@NgModule({
    imports: [
        CommonModule,
        FormsModule
    ],
    declarations: [
    ],
    exports: [
    ],
    providers: [
        BasePolicyService
    ]
})
export class PolicyModule {}