import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { UiSwitchModule } from 'ngx-ui-switch';
import { BusyModule } from 'angular2-busy';
import { UtilsModule } from './../../utils/index';
import { ExpertsTableComponent } from './table/expertsTable.component';
import { ExpertDetailsInfoComponent } from './details/info/expertDetailsInfo.component';
import { ExpertDetailsEditComponent } from './details/edit/expertDetailsEdit.component';
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
        Ng2Bs3ModalModule,
        UiSwitchModule,
        BusyModule,
    ],
    exports: [
        ExpertsComponent,
        ExpertsTableComponent,
        ExpertDetailsInfoComponent,
        ExpertDetailsEditComponent,
    ],
    declarations: [
        ExpertsComponent,
        ExpertsTableComponent,
        ExpertDetailsInfoComponent,
        ExpertDetailsEditComponent,
    ],
    providers: []
})
export class ExpertsModule {}
