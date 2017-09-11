import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';

import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';

import { UtilsModule, WorkingHoursComponent } from './../../utils/index';
import { ExpertsTableComponent } from './table/expertsTable.component';

import { ExpertsComponent } from './experts.component';
import { routing } from './experts.routing';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule
    ],
    exports: [
        ExpertsComponent,
        ExpertsTableComponent
    ],
    declarations: [
        ExpertsComponent,
        ExpertsTableComponent
    ],
    providers: []
})
export class ExpertsModule {}
