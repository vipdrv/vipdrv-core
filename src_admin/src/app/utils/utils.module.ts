import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../app.translation.module';

import { ImageCropperComponent } from 'ng2-img-cropper';

import { UiSwitchModule } from 'ngx-ui-switch'

import { ConsoleLogger } from './logging/console/console.logger';
import { PromiseService } from './promises/promise.service';

import { WorkingHoursComponent } from './components/working-hours/advanced/workingHours.component';
import { WorkingHoursSimpleComponent } from './components/working-hours/simple/workingHours.simple.component';

import { ImageSelectComponent } from './components/imageSelect/imageSelect.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        UiSwitchModule
    ],
    declarations: [
        WorkingHoursComponent,
        WorkingHoursSimpleComponent,
        ImageCropperComponent,
        ImageSelectComponent
    ],
    exports: [
        WorkingHoursComponent,
        WorkingHoursSimpleComponent,
        ImageCropperComponent,
        ImageSelectComponent
    ],
    providers: [
        ConsoleLogger,
        PromiseService
    ]
})
export class UtilsModule {}