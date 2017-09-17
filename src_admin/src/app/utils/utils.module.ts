import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../app.translation.module';

import { ImageCropperComponent } from 'ng2-img-cropper';

import { AuthorizationManager } from './auth/authorization.manager';
import { HttpService } from './http/http.service';
import { ConsoleLogger } from './logging/console/console.logger';
import { PromiseService } from './promises/promise.service';

import { WorkingHoursComponent } from './components/working-hours/workingHours.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule
    ],
    declarations: [
        WorkingHoursComponent,
        ImageCropperComponent
    ],
    exports: [
        WorkingHoursComponent,
        ImageCropperComponent
    ],
    providers: [
        AuthorizationManager,
        HttpService,
        ConsoleLogger,
        PromiseService
    ]
})
export class UtilsModule {}