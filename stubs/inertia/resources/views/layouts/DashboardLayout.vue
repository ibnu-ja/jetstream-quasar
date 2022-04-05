<script lang="ts" setup>
import { Head, Link as InertiaLink, usePage } from '@inertiajs/inertia-vue3'
import { Jetstream, Team, User } from '@/scripts/types/inertia-props'
import { computed, inject, ref } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import Progress from './Progress.vue'
const leftDrawerOpen = ref(false)

function toggleLeftDrawer () {
  leftDrawerOpen.value = !leftDrawerOpen.value
}

const route: any = inject('route')

const user = computed(() => usePage().props.value.user as User)
const jetstream = computed(() => usePage().props.value.jetstream as Jetstream)

function switchToTeam (team: Team) {
  Inertia.put(route('current-team.update'),
    {
      team_id: team.id
    },
    {
      preserveState: false
    })
}
function logout () {
  Inertia.post(route('logout'))
}

defineProps<{ title: string }>()
</script>

<template>
  <Head :title="title" />
  <Progress />
  <q-layout view="hhh lpR fff">
    <q-header
      :bordered="!$q.dark.isActive"
      :elevated="$q.dark.isActive"
      :class="[$q.dark.isActive ? 'bg-primary text-white' : 'bg-white text-primary']"
    >
      <q-toolbar>
        <q-btn
          flat
          round
          icon="mdi-menu"
          aria-label="Menu"
          @click="toggleLeftDrawer"
        />

        <q-toolbar-title>{{ title }}</q-toolbar-title>

        <div>Quasar v{{ $q.version }}</div>
      </q-toolbar>
    </q-header>

    <q-drawer
      v-model="leftDrawerOpen"
      show-if-above
      bordered
    >
      <q-img
        class="absolute-top"
        src="https://cdn.quasar.dev/img/material.png"
        style="height: 150px"
      >
        <div class="absolute-bottom bg-transparent">
          <q-avatar
            size="56px"
            class="q-mb-sm"
          >
            <img :src="user.profile_photo_url">
          </q-avatar>
          <div
            class="text-weight-bold"
            v-text="user.name"
          />
          <div v-text="user.email" />
        </div>
      </q-img>
      <q-scroll-area
        style="height: calc(100% - 150px); margin-top: 150px; border-right: 1px solid #ddd"
      >
        <q-list id="nav">
          <q-item-label
            header
            class="q-pa-sm"
          >
            Home
          </q-item-label>
          <inertia-link
            class="nostyle"
            :href="route('dashboard')"
          >
            <q-item
              clickable
              active-class="bg-primary-active"
              :class="{ 'bg-primary-active': route().current('dashboard') }"
              :active="route().current('dashboard')"
            >
              <q-item-section avatar>
                <q-icon name="mdi-view-dashboard" />
              </q-item-section>

              <q-item-section>
                <q-item-label>Dashboard</q-item-label>
              </q-item-section>
            </q-item>
          </inertia-link>
          <inertia-link
            v-if="jetstream.hasApiFeatures"
            class="nostyle"
            :href="route('api-tokens.index')"
          >
            <q-item
              clickable
              active-class="bg-primary-active"
              :class="{ 'bg-primary-active': route().current('tokens.index') }"
              :active="route().current('api-tokens.index')"
            >
              <q-item-section avatar>
                <q-icon name="mdi-api" />
              </q-item-section>

              <q-item-section>
                <q-item-label>API Tokens</q-item-label>
              </q-item-section>
            </q-item>
          </inertia-link>
          <q-separator />
          <template v-if="jetstream.hasTeamFeatures">
            <q-item-label
              header
              class="q-pa-sm"
            >
              Manage Team
            </q-item-label>

            <inertia-link
              class="nostyle"
              :href="route('teams.show', user.current_team)"
            >
              <q-item
                link
                :active="route().current('teams.show')"
                active-class="bg-primary-active"
                :class="{ 'bg-primary-active': route().current('teams.show') }"
                clickable
                active-color="primary"
              >
                <q-item-section avatar>
                  <q-icon name="mdi-account-multiple-outline" />
                </q-item-section>
                <q-item-section>
                  <q-item-label>
                    Team Settings
                  </q-item-label>
                </q-item-section>
              </q-item>
            </inertia-link>

            <inertia-link
              class="nostyle"
              :href="route('teams.create')"
            >
              <q-item
                v-if="jetstream.canCreateTeams"
                active-class="bg-primary-active"
                :class="{ 'bg-primary-active': route().current('teams.create') }"
                clickable
                :active="route().current('teams.create')"
              >
                <q-item-section avatar>
                  <q-icon name="mdi-account-multiple-plus-outline" />
                </q-item-section>

                <q-item-section>
                  <q-item-label>Create New Team</q-item-label>
                </q-item-section>
              </q-item>
            </inertia-link>
            <q-item-label
              header
              class="q-pa-sm"
            >
              Switch Teams
            </q-item-label>
            <template
              v-for="team in user.all_teams"
              :key="team.id"
            >
              <q-item
                clickable
                @click.prevent="switchToTeam(team)"
              >
                <q-item-section
                  v-if="team.id == user.current_team_id"
                  avatar
                >
                  <q-icon
                    name="mdi-check-circle-outline"
                    color="positive"
                  />
                </q-item-section>
                <q-item-section>
                  <q-item-label v-text="team.name" />
                </q-item-section>
              </q-item>
            </template>
            <q-separator />
          </template>

          <q-item-label
            header
            class="q-pa-sm"
          >
            Account
          </q-item-label>

          <inertia-link
            class="nostyle"
            :href="route('profile.show')"
          >
            <q-item
              clickable
              active-class="bg-primary-active"
              :class="{ 'bg-primary-active': route().current('profile.show') }"
              :active="route().current('profile.show')"
            >
              <q-item-section avatar>
                <q-icon name="mdi-account" />
              </q-item-section>

              <q-item-section>
                <q-item-label>Profile</q-item-label>
              </q-item-section>
            </q-item>
          </inertia-link>
          <q-item
            clickable
            @click.prevent="logout"
          >
            <q-item-section avatar>
              <q-icon name="mdi-logout" />
            </q-item-section>
            <q-item-section>
              <q-item-label>Logout</q-item-label>
            </q-item-section>
          </q-item>
        </q-list>
      </q-scroll-area>
    </q-drawer>

    <q-page-container>
      <slot />
    </q-page-container>
  </q-layout>
</template>

<style lang="scss">
#nav {
  div.q-item {
    margin-left: 6px;
    margin-right: 6px;
    margin-bottom: 4px;
    border-radius: 5px;
    min-height: 38px;
    padding: 4px 8px;
  }
  div.q-item__label {
    max-width: 100%;
    font-size: 0.8125rem;
  }
  div.q-item__section--avatar {
    min-width: 32px;
  }
}
</style>
