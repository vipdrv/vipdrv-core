import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { UtilsModule } from './../../utils/index';
import { AuthorizationService } from './authorization.service';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        UtilsModule
    ],
    declarations: [
    ],
    exports: [
    ],
    providers: [
       AuthorizationService
    ]
})
export class AuthorizationModule {}