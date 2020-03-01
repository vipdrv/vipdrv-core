import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { AppTranslationModule } from '../../app.translation.module';
import { NgaModule } from '../../theme/nga.module';
import { Ng2Bs3ModalModule } from 'ng2-bs3-modal/ng2-bs3-modal';
import { PaginationModule } from 'ng2-bootstrap';
import { BusyModule } from 'angular2-busy';
import { TextMaskModule } from 'angular2-text-mask';
import { UiSwitchModule } from 'ngx-ui-switch';
import { UtilsModule } from './../../utils/index';
import { SitesTableComponent } from './table/sitesTable.component';
import { BeveragesModule } from './../beverages/index';
import { ExpertsModule } from './../experts/index';
import { RoutesModule } from './../routes/index';
import { SiteDetailsEditComponent } from './details/edit/siteDetailsEdit.component';
import { SiteDetailsRelationsComponent } from './details/relations/siteDetailsRelations.component';
import { SiteOverviewComponent } from './overview/siteOverview.component';
import { SiteContactsComponent } from './contacts/siteContacts.component';
import { StepsTableComponent } from './stepWizard/step-wizard.component';
import { SitesComponent } from './sites.component';
import { routing } from './sites.routing';

import { SiteCardsComponent } from './cards/siteCards.component';
import { SiteCardComponent } from './cards/siteCard.component';

@NgModule({
    imports: [
        CommonModule,
        FormsModule,
        AppTranslationModule,
        NgaModule,
        routing,
        UtilsModule,
        Ng2Bs3ModalModule,
        PaginationModule,
        BusyModule,
        UiSwitchModule,
        BeveragesModule,
        ExpertsModule,
        RoutesModule,
        TextMaskModule
    ],
    exports: [
        SitesComponent,
        SitesTableComponent,
        SiteCardsComponent,
        SiteCardComponent,
        SiteDetailsEditComponent,
        SiteDetailsRelationsComponent,
        SiteOverviewComponent,
        SiteContactsComponent,
        StepsTableComponent,
    ],
    declarations: [
        SitesComponent,
        SitesTableComponent,
        SiteCardsComponent,
        SiteCardComponent,
        SiteDetailsEditComponent,
        SiteDetailsRelationsComponent,
        SiteOverviewComponent,
        SiteContactsComponent,
        StepsTableComponent,
    ],
    providers: []
})
export class SitesModule {}


