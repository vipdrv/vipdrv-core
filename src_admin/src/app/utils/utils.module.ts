import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../app.translation.module';

import { AuthorizationManager } from './auth/authorization.manager';
import { HttpService } from './http/http.service';
import { ConsoleLogger } from './logging/console/console.logger';

import { WorkingHoursComponent } from './components/working-hours/workingHours.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule
    ],
    declarations: [
        WorkingHoursComponent
    ],
    exports: [
        WorkingHoursComponent
    ],
    providers: [
        AuthorizationManager,
        HttpService,
        ConsoleLogger
    ]
})
export class UtilsModule {}